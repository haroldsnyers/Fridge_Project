# Fridge Website - NO Waste

## Introduction
This is project for the course Web architecture at the ECAM. I was first asked to make a website using symfony and a MVC architecture. In this part the backend and frontend was managed by symfony. Using the MVC architecture, views were rendered using twig. 
In the second part of the project I was asked to use an SPA architecture for the frontend, namely with the framework Angular. 

## Presentation of the Website
This website is a Fridge repository which enables the user to register and track the food they have in their fridge. Each user has a login and is able to add fridges to their repository. Each fridge is made of floors which contain food of a certain type or more. 

In the future of the website, some features could be added like adding a recipe book. In addition to that, enable a feature which is capable of searching recipes containing food you have in your fridge.

## The project
The last version of this project uses SPA using symfony as backend and Angular as frontend. 

### Setup SPA

#### Database configuration
+ Create a mysql account
+ go the .env file and next to 'DATABASE_URL' put 
```bash
mysql://<username>:<password>@<host>:<port>/<databaseName>
```

#### Symfony
You can either start the backend server using phpStorm 'run' command with the right configuration, namely 'php Built-in Web Server'.
You will need to setup the host and port an indicate the path to the public file of the symfony project.
The other way, is to start the server using the command line: 
```bash
cd backend-symfony
symfony server:start
```

#### Angular
Using the command line you can satrt the angular project 
```bash
cd fridge-fronte
ng serve
```
