#ulphp
##nginx部署

    location /project/ {
        index  index.html index.htm index.php l.php;
        autoindex  off;
    
        if (!-e $request_filename){
            rewrite  ^/project/(.*)$  /project/index.php?s=/$1  last;
        }
    }