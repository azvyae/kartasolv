# Kartasolv v.1.3.7

Kartasolv is an Social Welfare Development Web Application that focused on helping social organization to help and organize data, especially in Sarijadi Urban Village. This particular web application will be used for my campus final project and developed for Karang Taruna Sarijadi themself, to provide information, and manage PMKS & PSKS data in Sarijadi with real time control 🙏.

## Overview
You can access our Website on :<br>
https://kartasarijadi.com

If you would like to see full documentation to make this kind of project, you can see<br>
https://kartasarijadi.com/api

This project made with some third party tech stack other than [PHP](https://php.net) and [Codeigniter 4](https://codeigniter.com/).
Those tech stacks listed below:

| Tech Name | Description |
|:---------:|-------------|
| [Composer](https://getcomposer.org/) | Main dependency manager for PHP and installing Codeigniter 4, also this package manager is used for running tests, and import some third party libraries. |
| [NPM](https://www.npmjs.com/) | Basicly this is used for importing Bootstrap 5 Node Modules to be customized with using SASS. |
| [MySQL](https://www.mysql.com/) | MySQL used for developing through localhost function, but when it comes to deployment to the server, it uses MariaDB. But still, it's the same with different license. |
| [Bootstrap 5](https://getbootstrap.com) | Is used for main framework of basic HTML, CSS, JS display and design templates. |
| [Jquery](https://api.jquery.com/) | Basicly I didn't use much of Jquery because most of the time, i use native Javascript functions to manipulate html objects. So, Jquery is used for AJAX call and it's Datatables dependency, so I still need that. |
| [SwiperJS](https://swiperjs.com/) | Touch and automatic slider, used for displaying scrolling Organization members/personel on the landing page. |
| [PHPDocumentor](https://docs.phpdoc.org/) | Generating documentation |
| [PHPUnit](https://phpunit.readthedocs.io/) | Actually, Codeigniter 4 has imported PHPUnit properties and methods for asserting data. Codeigniter 4 also have full documentation for Unit Testing. This PHPUnit itself is used for HTTP Call unit testing by asserting main MVC functions |
| [HashIds](https://hashids.org/) | Hashids is used for generate encoded and decoded ID shown in the Web Application |
| [PHPOffice](https://phpspreadsheet.readthedocs.io/en/latest/) | PHPOffice, or mainly PHPSpreadsheet is used for importing xls and xlsx file to the PMKS and PSKS table. |
| [Recaptcha V3](https://developers.google.com/recaptcha/docs/v3) | This web application also have spam prevention based on Recaptcha Score. |
| [Datatables](https://datatables.net/) | Datatables used for displaying table data seamlessly in one page, with server side processing through Ajax requests. |

## Features latest v.1.3.7

Here are lists of implemented features through this Kartasolv Web Application.

### 1. Organizational Profile and History
Landing page is used for displays main information about this organization. The first page has 4 main sections, and second page (history) also has 4 sections that could be customized by the Administrator.

On the first page, details about every section is shown below:
1. Hero, that displays landing tagline and its logo.
2. Organization vision and mission, with its image.
3. Organization activities, with semi hard coded three activities that Administrator could update periodically.
4. Lastly we have organization members/personels.

Organizational profile also provided Call to Action url & text, that if you fill this input, it will show Call to Action button based on the input. Otherwise it will shows nothing. You can change Organization and History information after logged in as an Administrator. You can access that page on the left sidebar menu.

### 2. Messages
There is page to Contact the organization, through message input and shown Google Maps location on the page. You can also receive messages and see/manage incoming messages after logged in. Every messages sent also sent to Administrator email. Every messages filtered by the automatic Google Recaptcha V3.

### 3. Authentication
Every user could login, try to reset password with forget password procedure. This function protected by Google Recaptcha V3 scoring.

### 4. Profile
Every logged in user can change its email, and password, also basic profile information like name to be shown in the navigation bar.

### 5. Manage Members Data
Members data shown by the Datatables, you could do add, change, and multiple delete data shown in the table. Every image uploaded through this system will be compressed and converted into webp.

### 6. Manage PMKS and PSKS data
Administrator could add, change, multiple delete, multiple manipulation, and also could upload spreadsheet data with customized templates provided in the resources/ folder, for PMKS and PSKS data.
