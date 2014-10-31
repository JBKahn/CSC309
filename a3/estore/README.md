Assignment 3

eStore

Due Date: Nov 17 at 5:00 PM

Description

Develop a web front for a fictional electronic store that sells collectible Baseball cards. Your site should include the following features:

Create new account.
User login.
Browse inventory.
Add a card to shopping cart.
Edit shopping cart content's (change item quantity, delete items)
Checkout. This function should collect payment information (credit card number and expiry date) and display a printable receipt (a simple example that shows how to print from JavaScript is available here).
Email receipt to customer. Documentation for sending email from CodeIgniter is available here. To use this feature you will need access to a public SMTP server. One possibility is to use the SMTP server provided by Gmail.
Requirements

You application most comply with the following requirements:

Implement your application using all of the following technologies: PHP, CodeIgniter, HTML, CSS, JavaScript, JQuery, and DOM. Use CSS style sheets in favor of deprecated HTML tags and attributes.
For simplicity, assume that only 1 customer will use the site at any given time, i.e., there is no need to implement concurrency control for this assignment.
Assume that there is a unlimited amount of items available for any product sold on the site, i.e., you will never run out of a given item.
Your site should include an administrator page, which should be accessible by login into the site using the "admin" username and a password. The administrator page should enable the administrator to perform the following tasks:
Add, delete and edit products
Display all finalized orders
Delete all customer and order information
Your application should be easy to use.
The main entry page for your site should be called index.php.
You assignment can target any one browser of your choosing. The TAs have access to desktop versions of Firefox and Chrome. If you use any other browser, make sure you make it available as part of your AMI.
Assume a fixed browser window size.
Store all your data on a MySql database that conforms to the schema shown in Figure 1. An sql script for creating the database is available here. To help you get started, I am providing you with an AMI (ami-5cc44034) that has a copy of this database, as well as the popular tool mysql-workbench. The root password for the MySql instance installed on the AMI is sp2014. Important: Don't make changes to the database's schema (e.g., add tables, change attribute names). The TA will use their own version of the database to grade your assignment and if you modify the schema, your application won't work.
Each item offered by the store should include a picture.
The shopping cart should show the total cost for the items selected.
For unfinalized orders, store shopping cart information on the server using a PHP session. Store information in the provided database only once orders are finalized.
All data provided by the user should be validated. Enforce the following validation rules:
Credit card number must have 16 digits
Expiration date should be a valid date (MM/YY)
The card should not have expired
All fields in the customer profile should not be null
Customer email most conform to a valid email format
Password most be at least 6 characters long

Figure 2: Database Schema
To help you get started, I have created a sample CodeIgniter application called estore that provides simple functionality for manipulating the products table. This application is also pre-installed in the provided AMI at /var/www/html/estore. To use the application, start a browser and navigate to http://server_name/estore

Deliverables

Your assignment should be submitted as an AMI (Amazon Machine Image) that includes (at a minimum) an Apache web server, and all the files for your web site (HTML, CSS, Javascript, Images, PHP).

Using MarkUs, submit a README file with the following information:

The ID of your AMI
Location of your source files within the AMI
Any necessary instructions for starting Apache
Details about the browser the TA should use to test your assignment
Documentation for your Web site. Include a brief explanations of how it all works, e.g., list of main user-defined objects, and datastructures.
Note: Do not submit a copy of your database and do not include your SMTP server credentials in your submission. The TAs will use their own.

Finally, all deliverables should be neatly formatted, readable,and be properly documented .

Good luck!
