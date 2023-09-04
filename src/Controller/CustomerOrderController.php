<?php

namespace App\Controller;

use App\Entity\CustomerOrder;
use App\Entity\CustomerOrderItem;
use App\Entity\DeliveryOption;
use App\Entity\Item;
use Doctrine\Migrations\Configuration\Migration\Exception\JsonNotValid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CustomerOrderController extends AbstractController
{

    #[Route('/api/orders', name: 'order_list', methods: ['GET', 'HEAD'])]
    public function list(
        EntityManagerInterface $entityManager,
        Request $request): JsonResponse
    {
        $criteria = [];

        $params = $request->query->all();

        if ($id = $params['id'] ?? null)
        {
            $criteria = [
                'id' => $id
            ];
        } elseif ($status = $params['status'] ?? null)
        {
            $criteria = [
                'status' => $status
            ];
        } else
        {
            throw new BadRequestException();
        }

        $orders = $entityManager->getRepository(CustomerOrder::class)->findBy($criteria);

        // Temporary method would be replaced with serializers, not sure where those go with fresh clean symfony,
        // used to using api-platform, and would take alot of fresh config.
        $response = [];
        foreach ($orders as $order) {
            $orderData = [
                'id' => $order->getId(),
                'reference' => $order->getReference(),
                'name' => $order->getCustomerName(),
                'address' => $order->getCustomerAddress(),
                'status' => $order->getStatus(),
                'deliveryOption' => $order->getDeliveryOption()->getName(),
                'items' => []
            ];
            foreach ($order->getCustomerOrderItems() as $item) {
                $itemData = [
                    'item' => $item->getItem()->getName(),
                    'itemId' => $item->getItem()->getId(),
                    'quantity' => $item->getQuantity(),
                    'price' => $item->getPrice(),
                    'total' => $item->getTotal()
                ];
                $orderData['items'][] = $itemData;
            }
            $response[$order->getId()] = $orderData;
        }

        return new JsonResponse($response);
    }

    #[Route('/api/orders', methods: ['POST'])]
    public function createOrder(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'] ?? null;
        $address = $data['address'] ?? null;
        $deliveryOptionId = $data['delivery_option'] ?? null;
        $items = $data['items'] ?? null;

        if (
            !$name ||
            !$address ||
            !$deliveryOptionId ||
            !$items ||
            !is_array($items) ||
            !$this->validateItemsArray($items)
        ) {
            throw new JsonNotValid();
        }

        $deliveryOption = $entityManager->getRepository(DeliveryOption::class)->find($deliveryOptionId);
        $estimatedDeliveryDate = new \DateTime('now');
        $estimatedDeliveryDate->modify('+' . $deliveryOption->getLeadDays() . ' day');

        // Generate a reference, would be a more standard method for these
        $reference = uniqid();

        // Create a new order
        $order = new CustomerOrder();
        $order->setCreatedAt(new \DateTime())
            ->setReference($reference)
            ->setCustomerName($name)
            ->setCustomerAddress($address)
            ->setStatus('processing')
            ->setDeliveryOption($deliveryOption)
            ->setEstimatedDeliveryDateTime($estimatedDeliveryDate);
        $entityManager->persist($order);

        // Add the items
        foreach ($items as $item) {
            $itemEntity = $entityManager->getRepository(Item::class)->find((int)$item['id']);
            if ($item) {
                $itemRow = new CustomerOrderItem();
                $itemRow->setCustomerOrder($order)
                    ->setItem($itemEntity)
                    ->setPrice($itemEntity->getPrice()) // Snapshot the price, in case it changes later
                    ->setQuantity($item['quantity']);
                $entityManager->persist($itemRow);
            }
        }

        $entityManager->flush();

        return $this->json([
            'order' => $order
        ]);
    }

    // Ideally this would be /{id} but test requested both vars come in the payload
    #[Route('/api/orders', name: 'order_update', methods: ['PATCH'])]
    public function update(
        EntityManagerInterface $em,
        Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $id = $data['id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$id || !$status) { // Should also check valid statuses
            throw new JsonNotValid();
        }

        $order = $em->getRepository(CustomerOrder::class)->find($id);

        if (!$order) {
            throw new BadRequestException();
        }

        $oldStatus = $order->getStatus();
        // Set new status
        $order->setStatus($status);
        $em->flush();

        $response = [
            'oldStatus' => $oldStatus,
            'newStatus' => $status,
        ];

        return new JsonResponse($response);
    }

    protected function validateItemsArray(array $items): bool
    {
        $required_keys = [
            'id',
            'quantity'
        ];

        foreach ($items as $item) {
            foreach ($required_keys as $required_key) {
                if (!isset($item[$required_key])) {
                    return false;
                }
            }
        }

        return true;
    }

}
