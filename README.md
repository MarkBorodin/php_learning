## INSTALL APP

##APP LEARNING

Blog.  

On the site you can view and create posts, comments to them. Authorization is available. There is an administrative panel.  

Anonymous user.  

On the main page of the site, it is possible to view posts, comments to them and sort posts by categories. 
You can go through authorization or registration.

Authorized user.  

The same things are available as for an anonymous user + it is possible to  create posts and comment on them.
It is possible to edit only your posts. It's impossible to edit comments (even your own). 
In your personal account, it is possible to change your data and password.

Admin user.   

The site administrator can do the same as the authorized user. But the admin panel is also available for the administrator.
There it is possible to:  

create, delete, edit (all) posts,  
create, delete, edit comments,  
create, delete, edit categories,  
create, delete, edit users  
(CRUD operations with all models).

The administrative panel was created from scratch, without the use of ready-made solutions.

### Setup

clone repository:
```
git clone https://github.com/MarkBorodin/php_learning.git
```
move to folder "php_learning":
```
cd php_learning
```

run db in docker-compose:
```
docker-compose up
```

install reqirements:
```
composer install
```

migrate:
```
bin/console doctrine:migrations:migrate
```

create super user:
```
php bin/console app:createsuperuser username email password
```
(where:
username - your user name;
email - your email;
password - your password)

run:
```
symfony server:start
```

### Finish
