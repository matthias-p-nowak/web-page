# Purpose

A simple web-site allowing a handful of editors to edit text elements via the web. After a login, they are able to change the text directly on the web-page, edit the content in a simple Word-like editor, upload media and edit basic page styles. Edits on common elements are flushed to all pages.

All complicated stuff like consistent styling, adding editors, initial configuration is left to a designer who uploads the relevant files via other means.

The system also has a basic rewind feature allowing the rollback of recent edits.

## Comparison to WordPress, SiteJet, Website-builder, etc.

Those complex tools claim to give a user the ability to create websites with just a few clicks. Often the user is overwhelmed with a plentora of templates, which do not fit the purpose. Adapting the template requires a deep understanding how those tools work and that exceeds the knowledge of a common user. 
Web sites that display nicely on phones, tablets and desktops demand a rather intricate design, which requires competance. Often this design competance is replaced with a required tool competance, when one uses Wordpress. At the end, clients are overwhelmed by the demands, leave the setup to a wordpress specialist and then only carry out some amendments.

This project aims at reducing the required knowledge to a level compared to Word for simple edits, while configuration and overall styling is left to a designer/administrator.

## Approach

Writing formatted text requires no more knowledge than Office Word demands. This should be available to editors.

Layout, styles like font families, font sizes, font style, colors and similar are often left to a designer, since their configuration in style files requires knowledge about how the web actually works. Even template based tools like the ones mentioned above do not make it easier. Instead of common css knowledge, one needs to know about the inner workings of the tools and templates.

User administration does not justify advanced management system, since those functions are rarely used.

## Installation

This project provides the basic stuff to get a simple example web-page up and running. 

Folder  | Purpose
--- | ---
`private` | contains the engine for web site editing
`public/js` | contains the few java scripts necessary for editing
`public/media` | designated folder for uploading media
`public/tinymce` | must contain the tinemce.min.js - must be downloaded separately
`public/favicon.ico` | the web-sites icon, must be uploaded and replaced separately
`public/*.html` | all the different pages
`public/main.css` | have this or a similar style file for the web-site
`scss` | contains the scss source files, `admin.scss` is required for editing, the remaining *scss* files can be changed freely.

### Creating the style file



### Copying folders

Find out where your base html directory is! It often is name *www*. Create a simple `index.html` file and confirm that this can be access from the web-browser.

### Download *tinymce*

TinyMCE is a javascript library that contains a wisywig editor. Download from [tinymce webpage](https://www.tiny.cloud/get-tiny/) and unpack the zip-file. The *tinymce* folder that contains the `tinymce.min.js` file needs to be copied onto the accessible webpage




## Overview

The following outlines how the system works. 

### Editors

A fixed set of editors can login and then select one of the few actions

- login via top right box
    - presents the conventional login box
    - if password is empty a new password is mailed to the email address, if that one is among the list of editors
- configure a page
    - change the file name of the page
    - duplicate the page
    - delete a page
    - edit the title
    - edit the description
    - give an overview of all pages
- manage uploaded pictures
    - upload new ones
    - delete
    - rename filename
- right click on elements and edit the html content
- logout

### Designers and site manager

They have direct access to the files via ftp or similar. Hence, they can change everything. However, there is no support via the web unlike WordPress & Co.