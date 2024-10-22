<?php

namespace Gartservice\LivewireSweetAlert;

use Illuminate\Support\Str;

trait LivewireSweetAlert
{
    protected array $confirmTokens = [];

    public function alert(
        string $title,
        string $html,
        string $icon = 'success',
        array $options = [] // Optional SweetAlert options like didClose, willOpen
    ): void {
        $jsOptions = json_encode(array_merge([
            'title' => $title,
            'html' => $html,
            'icon' => $icon,
        ], $options));

        $this->js("Swal.fire($jsOptions);");
    }

    public function confirm(
        string $title,
        string $html,
        string $icon = 'question',
        string $confirmText = 'Confirm',
        string $cancelText = 'Cancel',
        string $onConfirmMethod = 'confirmed',
        string $onCancelMethod = 'cancelled',
        array $callbacks = [] // Optional: didClose, didOpen, willOpen, etc.
    ): void {
        $token = Str::random(12);
        $this->confirmTokens[$token] = $onConfirmMethod;

        $jsCallbacks = collect($callbacks)->map(fn($body, $hook) => "$hook: function() { $body }")
                                        ->implode(",\n");

        $js = <<<JS
                Swal.fire({
                    title: `$title`,
                    html: `$html`,
                    icon: `$icon`,
                    confirmButtonText: `$confirmText`,
                    cancelButtonText: `$cancelText`,
                    showCancelButton: true,
                    allowEscapeKey: true,
                    focusCancel: true,
                    $jsCallbacks
                }).then((result) => {
                    if (result.isConfirmed) {
                        \$wire.handleConfirmedAction('$token');
                    } else {
                        \$wire.$onCancelMethod();
                    }
                });
                JS;

        $this->js($js);
    }

    public function handleConfirmedAction(string $token): void
    {
        if (!isset($this->confirmTokens[$token])) {
            $this->alert('Invalid token', 'Confirmation token mismatch.', 'error');
            return;
        }

        $method = $this->confirmTokens[$token];
        unset($this->confirmTokens[$token]);

        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    public function loading(): void
    {
        $this->js('Swal.showLoading();');
    }

    public function jsonToHtml(mixed $json): string
    {
        $formatted = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return "<pre>{$formatted}</pre>";
    }
}
