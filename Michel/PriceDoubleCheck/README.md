## GITHUB LINK
- [LINK](https://github.com/ssuhss/Michel_PriceDoubleCheck)

## PHP VERSION
- PHP 8.1.2

## PHPUNIT VERSION
- 9.5.27

## Magento version
- 2.4.5-p1 

### TABLE NAME
- price_approve

### CLI COMMANDS
- bin/magento michel:price:list

- bin/magento michel:price:approve --sku 24-MB03
- bin/magento michel:price:approve --id 17

- bin/magento michel:price:reprove --sku 24-MB03
- bin/magento michel:price:reprove --id 17

### GRAPHQL ENDPOINTS (CURL)
- LIST


- - curl --location --request POST 'http://m2.local.com.br/graphql' \
--header 'Content-Type: application/json' \
--data-raw '{"query":"query {\n    approvePriceList{\n        items{\n            id,\n            name,\n            sku,\n            datetime,\n            attribute_code,\n            price_before,\n            price_after_approve\n        }\n        \n    }\n}","variables":{}}'


- APPROVE


- - curl --location --request POST 'http://m2.local.com.br/graphql' \
--header 'Content-Type: application/json' \
--data-raw '{"query":"mutation ($input: ApprovePriceInput) {\n    approvePrice(input: $input){\n        message\n    }\n}","variables":{"input":{"id":8}}}'


- SAVE NEW PRICE 


- - curl --location --request POST 'http://m2.local.com.br/graphql' \
--header 'Content-Type: application/json' \
--data-raw '{"query":"mutation ($input: ApprovePriceSaveInput) {\n    approvePriceSave(input: $input){\n        item{\n            id\n            name\n            sku\n            datetime\n            attribute_code\n            price_before\n            price_after_approve\n        }\n    }\n}","variables":{"input":{"sku":"24-WB03","price":"20.20"}}}'

### GRID
- ADMIN -> Michel Menu -> GRID Prices to approve

### Alert E-mail configuration
- ADMIN -> STORES -> Configuration -> Michel -> Price Approve

