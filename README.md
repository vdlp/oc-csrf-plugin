# Vdlp.Csrf

Adds CSRF protection.

## Requirements

* PHP 7.1 or higher

## Installation

*Composer:*

```
composer require vdlp/oc-csrf-plugin
```

*CLI:*

```
php artisan plugin:install Vdlp.Csrf
```

*October CMS:*

Go to Settings > Updates & Plugins > Install plugins and search for 'CSRF'.

## Configuration

Add the plugin configuration to your projects' config folder:

```
php artisan vendor:publish --provider="Vdlp\Csrf\ServiceProviders\CsrfServiceProvider" --tag="config"
```

Add the CSRF token to the head:

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

If you have any question about how to use this plugin, please don't hesitate to contact us at octobercms@vdlp.nl. We're happy to help you. You can also visit the support forum and drop your questions/issues there.
