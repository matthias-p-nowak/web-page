# Purpose

Many websites are unnecessarily overengineered for their primary purpose, which is to provide information about a company, project, or organization. Frequently, only a few pages covering a handful of topics are required. There's typically no necessity for a complex administration system or an abundance of plugins.

# Installation

The root folder of this project comprises three crucial components:

1. The *public* folder, intended to be placed where the web server can access it and serve its contents.
2. The *web-app* folder, ideally located outside the web root but still accessible to the PHP server process.
3. Auxiliary items such as the *Readme.md* file or the source files for *Sass*.

## Public

## WebApp

# Logins

The login button is hidden. Move the mouse to the left margin of the page, and the button is revealed.
The login is first via the 401 dialog by browser, and then displays a form.
Then a form might be displayed asking for the *email* address and a *magic number*. 
That magic number must be the same as in the `<site>.ini`-file from above. This prevents skript-kiddies from filling the database.
If correct, an email is sent to the user with the login details.
