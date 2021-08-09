## INSTALL APP

## APP LEARNING

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

First you need to clone the repository:
```
git clone https://github.com/MarkBorodin/php_learning.git
```
And go to the directory with the project. The entire command must be executed in this directory:
```
cd php_learning
```

***create a .env file and fill it with your data, using the .env.example file as an example***

Mysql is used as a database and it is started via docker-compose. To start the database, you need to run the command:
```
docker-compose up
```
(you can change to start the database in the docker-compose file. For example: specify your data for the parameters MYSQL_ROOT_PASSWORD, MYSQL_DATABASE and others). mysql: // root: db_password@127.0.0.1: 3306 / db_name? serverVersion = mariadb-10.4.8 "- replace db_password and db_name with MYSQL_ROOT_PASSWORD and MYSQL_DATABASE values))

Next, you need to install all the necessary packages, dependencies, and so on. This is done using the composer tool. To do this, you need to run the command:
```
composer install
```

The next step is to create all the required tables in the database. To do this, you need to perform a migration:
```
bin/console doctrine:migrations:migrate
```

In order to get into the admin panel of the site and start working on it, you need to create a super user. This is done using the following command:
```
php bin/console app:createsuperuser username email password
```
(where:  
username - your username;  
email - your email;  
password - your password)  

After that, you can start the server using the command:
```
symfony server:start
```
The server will run on a free port. The server address will be displayed in the console.

### Finish
