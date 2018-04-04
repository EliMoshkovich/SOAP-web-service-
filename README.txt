SOAP web service 
The web-service have 2 functions (endpoints):

List Coupons  (GET localhost:6543/coupons)
List all the coupons from the attached JSON

  
Clip Coupon (POST localhost:6543/coupons/{coupon_id})
receives coupon ID in URL.
add a column to every coupon in the coupons table, called clipped (boolean)
every time there's a clip, updating the coupons table with clipped = true. 
next time coupons are loaded, it's return clipped = true for the clipped coupons. 
return an error message if the user tries to clip the already-clipped coupon. 
