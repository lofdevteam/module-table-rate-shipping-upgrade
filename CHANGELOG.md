# Version 1.0.4 - 07/30/2021
- Upgrade version 1.0.4, add default value for column free_shipping = 0.0000
- Change controller import csv file, convert value of column price, weight_to, weight_from to float number before save

# Version 1.0.5 - 08/08/2021
- fix postal code on query find shipping rates
- remove unuse code
- refactor coding
- add new column for table "lof_marketplace_shippinglist": cost, cart_total
- column cost to calculate shipping cost
- cart_total will been check the current cart total with discount is equal or greater than cart_total value or not, then apply the shipping rates
- support zip code with charactor, number,...
- Updated more in the file: Model/Carrier.php
- New settings "Allow Free shipping for Zero Price", default = No. It allow apply free shipping when min shipping price in rate = 0, else the min shipping price should be greater than 0

# Version 1.0.5 - update 05/22/2022
- Compatible magento 2.4.4
- Support php 8
- Support disable bestway shipping rate (default table rate shipping) when it has rate = 0

# Version 1.0.6 - 09/06/2022
- Compatible magento 2.4.5
- Support php 8
- Fix common issues on backend and shopping cart
