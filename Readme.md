# Purpose

A simple web-site allowing a handful of editors to edit text elements via the web. 

## Comparison to WordPress, SiteJet, Website-builder, etc.

Those complex tools claim to give a user the ability to create websites with just a few clicks. Often the user is overwhelmed with a plentora of templates, which do not fit the purpose. Adapting the template requires a deep understanding how those tools work and that exceeds the knowledge of a common user.

## Approach

Writing formatted text requires no more knowledge than Office Word demands. This should be available to editors.

Layout, styles like font families, font sizes, font style, colors and similar are often left to a designer, since their configuration in style files requires knowledge about how the web actually works. Even template based tools like the ones mentioned above do not make it easier. Instead of common css knowledge, one needs to know about the inner workings of the tools and templates.

User administration does not justify advanced management system, since those function are rarely used.

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