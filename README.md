# OrdersApi

## Assume base_url is http://localhost/OrdersApi

## To save order details issue following post request to base_url+'/orders'
     Note mod rewrite should be enabled for following to work
     
     Request: http://localhost/OrdersApi/orders
     
     {
      "store_name":"product a",
      "order_amt":"400",
      "product_id":1,
      "email":"abc@gmail.com"
     }
     
     Response:
     {"status":"Success","message":"Order Inserted Successfully","order_id":4}
     
## To Send Order Confirmation mail asynchronously
     
     Before that add you gmail id and password in orders_mail.php file
     
      set a cron which runs every 5 minutes ,by doing crontab -e and add the following line 
        5  *    *    *    *  cd /var/www/html/OrderssApi/ && php orders_mail.php
