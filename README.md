# PHP MySQL examples for using non-Western characters
This is what I used to use to read or write to a MySQL server with PHP around 2016.
There are other ways to do it. But, this worked greatly especially with many different foreign languages.

Pay special attention to the following lines when you insert new data written in non-Latin, non-Western characters.

```PHP
mysqli_query($con,"SET CHARACTER SET 'utf8'");
mysqli_query($con,"SET SESSION collation_connection ='utf8_unicode_ci'");
mysqli_set_charset($con,"utf8"); ////////// important ////////
$area = mysqli_real_escape_string($con, trim($area));
```
