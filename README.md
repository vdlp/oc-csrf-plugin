# Vdlp.Csrf

Adds CSRF protection.

## Requirements

* PHP 8.0.2 or higher
* October CMS 2.0 or higher

## Installation

```
composer require vdlp/oc-csrf-plugin
```

## Configuration

Add the plugin configuration to your config folder:

```
php artisan vendor:publish --provider="Vdlp\Csrf\ServiceProviders\CsrfServiceProvider" --tag="vdlp-csrf-config"
```

Add the CSRF token to the `<head>` section:

```
<meta name="csrf-token" content="{{ csrf_token() }}">
```

Add the CSRF token to the AJAX requests:

```
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
</script>
```

## Questions? Need help?

If you have any question about how to use this plugin, please don't hesitate to contact us at octobercms@vdlp.nl. We're happy to help you.
