<?php

namespace Gartservice\LivewireSweetAlert;



trait LivewireSweetAlert
{
    protected array $confirmTokens = [];

    public function alert(
        string $title,
        string $html,
        string $icon = 'success',
        array $options = []
    ): void {
        $json = $this->prepareJsOptions([
            'title' => $title,
            'html'  => $html,
            'icon'  => $icon,
        ], $options);
        // dd($json);
        $this->js("Swal.fire($json);");
    }

    public function confirm(
        string $title,
        string $html,
        string $icon = 'question',
        string $confirmText = 'Confirm',
        string $cancelText = 'Cancel',
        string $onConfirmMethod = 'confirmed',
        string $onCancelMethod = 'cancelled',
        array $callbacks = []
    ): void {
        $payload = encrypt($onConfirmMethod);

        $json = $this->prepareJsOptions([
            'title' => $title,
            'html' => $html,
            'icon' => $icon,
            'confirmButtonText' => $confirmText,
            'cancelButtonText' => $cancelText,
            'showCancelButton' => true,
            'allowEscapeKey' => true,
            'focusCancel' => true,
        ], $callbacks);

        $this->js(<<<JS
            Swal.fire($json).then((result) => {
                if (result.isConfirmed) {
                    \$wire.handleConfirmedAction('$payload');
                } else {
                    \$wire.$onCancelMethod();
                }
            });
        JS);
    }

    public function handleConfirmedAction(string $encryptedMethodName): void
    {
        try {
            $method = decrypt($encryptedMethodName);
        } catch (\Exception $e) {
            $this->alert('Invalid token', 'Confirmation token could not be verified.', 'error');
            return;
        }

        if (method_exists(object_or_class: $this, method: $method)) {
            $this->$method();
        } else {
            $this->alert('Error', 'Callback method not found.', 'error');
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

    protected function prepareJsOptions(array $base, array $extra): string
    {
        $jsFunctionKeys = ['didOpen', 'didClose', 'willOpen', 'willClose', 'preConfirm', 'inputValidator'];

        $jsonOptions = [];
        $rawCallbacks = [];

        foreach (array_merge($base, $extra) as $key => $value) {
            if (in_array($key, $jsFunctionKeys) && preg_match('/^\s*(function\s*\(|\(?\s*\)\s*=>)/', $value)) {
                $rawCallbacks[$key] = $value;
            } else {
                $jsonOptions[$key] = $value;
            }
        }

        $json = rtrim(json_encode($jsonOptions, JSON_UNESCAPED_SLASHES), '}');

        foreach ($rawCallbacks as $key => $code) {
            $json .= ", \"$key\": $code";
        }

        return $json . '}';
    }
}
