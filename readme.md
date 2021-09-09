## Create a dummy user in wordpress

## Why
because you want to register someone with an email address and don't care about any other detail, because it's a dummy user.

## How
- unzip this into your wordpress plugins folder (I call mine "dummy-user").
- activate the plugin (there's no settings)
- add a [dummy-user] shortcode where you'd like your register form to appear. Like on a post or page.

## What
a form magically appears asking for an email address. It has the css class `du-register-form` and doesn't show anything if the user is logged in.

## Options
there's no formal settings for this plugin. it can produce two possible errors which appear in paragraph text below the form with css classnames `du-email-required` or `du-email-taken`. they aren't styled by default.

## License
MIT 
