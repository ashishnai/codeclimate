# Magento Module: arvato ComboDeals

Endpoint to provide combo deals feature in Magento.

## Features

- Create New Product type
    * Module Creation which extends from Bundle Product
    * Managing attributes which can be used for Combo Deals (sku,name,price)
- Combo Deal Set Tab
    * Display the new tab for Combo deal form
    * Provision of adding global fields like start , end date.
    * Grid for selecting simple products on add product click
    * Display selected simple products
    * Add inputs for discounts, quantity for the selected simple product
- Save Combo Deals (Add/Edit)
    * Logic for saving combo deal on add/edit
- Delete Combo Deals
    * Logic for deletion
- Store wise dependency
    * Start/End Date Time zone for different stores
    * Discount Price Currency for different stores
    * Translation for title/labels for different stores
- Display Grid
    * Display all combo deal products 
    * Filter combo deal products based on column values
    * Delete single or multiple combo deal product
    * View/Edit link for each combo deal
- System Configuration 
    * Global settings applicable on all the combo deal products