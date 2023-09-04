General notes
=============

Very strange working direct with a newer version of symfony, and without api_platform and other extensions.

I admit I struggled a little with the routing and setup using pure symfony, both due to using api-platform currently, and
the fact I use an older version of symfony. Also currently I started learning it on an existing codebase, rather than setting
one up from scratch, where I am used to working with them and fitting with their style and methods.

However if given the opportunity, I would work hard to learn and understand more about how to implement standard functionality
in symfony itself, in a way that fits with existing code. Hopefully I have been able to show a general knowledge of the data
design and functionality required to meet the requirements, even if I still need to expand my pure symfony knowledge.

General decisions / observations.
=================================

I have implemented with a basic sqlite db storage, to avoid any extra setup required for mysql, postgresql etc.

A few decisions were made based on this being standalone, which would not apply in a larger or existing system. However
I did not want to turn this into a 3 week test with growing scope.

- I have chosen to make order status a string, rather than an entity. While in a larger system it would be an entity,
to allow pipeline configuring between statuses and better indexing, that felt too complicated to add for this scope.

- Delivery options WERE created as an entity, to begin the idea of each one specifying it's lead-time, as an example.

- In a larger system, I would assume we would have customer records and associated address records, to allow the same user
and/or addresses to be re-used. Again this felt alot of work outside the initial scope.

- The same applies to Items, presumably this would fit inside a larger framework where products / items have existing
entities.

- On the PATCH api, i would have expected to send the id of the record on the url, and the status as a parameter. However
I have grabbed both from the params, as requested in the specification instead.

- On the GET it says to grab either an id or a status from the url parameters. Given it says to return the POST data as
part of the response, I assume that means to accept them as POST params.

- I have grabbed a copy of item prices during order creation, to protect against price changes while an order is in processing.

- I have also chosen to store the estimated delivery as part of the record. this could be a derived field, but could also be
useful to filter on.

- I added reference to order, as ideally this would change to use that as a lookup, rather than the auto-inc id, to avoid
any attacks increasing/decreasing the id value. I also named it customerOrder to avoid the reserved word, I know you can
work around it, but it causes so much pain.

- Validation / Serialization etc could be extended alot further, but I tried to keep this to the core basics for this test.
Extending to DTO, normalizers, custom filters etc seems to come down to alot of personal preference of how to work.

- The command is written using pure SQL, as by experience doctrine can get very slow on large batch processes. It could
however be redone to use a method on the repository if needed.
