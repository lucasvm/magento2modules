## What does this module do?

- This module will allow you to create featured products and insert them on any phtml.
- It will add a new item on the products admin to allow the user select or no to show as feature.

## How to install

- Copy and paste to app/code folder.

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```
- Copy block into phtml like this:
{{block class="Lansadigital\Featuredhome\Block\Home\FeaturedHome" name="featured_product_home" product_count="3" column_count="1" aspect_ratio="0" image_width="250" image_height="250" template="grid.phtml"}}

Porto Theme compatible.


## Lansadigital.com - Magento services.
Lucas Ansalone.
