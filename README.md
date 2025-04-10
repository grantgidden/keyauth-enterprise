# keyauth-enterprise
Source code for paid KeyAuth features

### Support

Join the Microsoft Teams!

[Join Microsoft Teams](https://teams.live.com/l/community/FAAU6KZ-TFq92fL8QE)

### **Bugs**

If the default example not added to your software isn't functioning how it should, please report a bug here https://keyauth.cc/app/?page=forms

However, we do **NOT** provide support for adding KeyAuth to your project. If you can't figure this out you should use Google or YouTube to learn more about the programming language you want to sell a program in.

### Make sure you do NOT do the following:

* Do not share this code with anyone
* Do not sell this code to anyone
* Do not create an auth service that competes with KeyAuth

### Requirements

System that is able to run PHP, MySQL, and Redis. Do note that the requirements to receive setup support though are that you are using a server with CentOS7 installed.

### Tutorial Videos:

**CentOS 7:** 

Currently I have no CentOS 7 tutorial released as I didn't have enough resources on my Oracle free tier account to make a CentOS 7 machine (as I already have one running for a different website), though if you would like to help installing on CentOS 7, mention `@networking` in the #setup-help channel of the Discord server

**Ubuntu 22.04**

Tutorial video: https://www.youtube.com/watch?v=jT5IVw2CzLY

Commands ran:

```sh
sudo nano /root/.ssh/authorized_keys
sudo apt update
sudo apt install nginx
sudo ufw app list
sudo ufw allow 'Nginx HTTP'
sudo apt install mysql-server
sudo mysql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password by 'mynewpassword';
quit
sudo apt install php8.1-fpm php-mysql
apt install firewalld
firewall-cmd --permanent --zone=public --add-port=80/tcp
firewall-cmd --reload
sudo apt update
sudo apt install redis-server
sudo systemctl restart redis.service
sudo apt-get update
sudo apt-get -y install php-redis
systemctl restart php8.1-fpm
systemctl restart nginx
mysql -u root -p
CREATE DATABASE main;
quit
mysql -u root -p main < db_structure.sql
sudo apt-get install php-curl
mysql -u root -p
USE main;
UPDATE `accounts` SET `role` = 'seller',`expires` = 2224663363 WHERE `username` = 'yourUsernameHere';
quit
git clone https://github.com/kingcc/cloudflare-ips-only.git
cd cloudflare-ips-only
sudo bash ./host.sh
```

For the automated installer:

Copy the repo into any folder, `cd` into the folder, and run these 2 commands:
```sh
chmod +x ./setup.sh
./setup.sh
```
