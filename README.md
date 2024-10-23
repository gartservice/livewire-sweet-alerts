# Livewire Sweet Alerts

A Laravel 11+ / Livewire 3 trait to easily trigger [SweetAlert2](https://sweetalert2.github.io/) modals from PHP code.

## 📦 Installation

```bash
composer require gartservice/livewire-sweet-alerts
```

## ⚙️ Setup

### 1. Include SweetAlert2 in your Blade layout

If not using Vite, add this to your main layout (e.g. `resources/views/layouts/app.blade.php`):

```html
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

### 2. Or install with Vite

```bash
npm install sweetalert2
```

Then in your `resources/js/app.js`:

```js
import Swal from 'sweetalert2';
window.Swal = Swal;
```

Run:

```bash
npm run dev
```

## 🚀 Usage

### Add Trait to Livewire Component

```php
use Gartservice\LivewireSweetAlert\LivewireSweetAlert;

class ExampleComponent extends Component
{
    use LivewireSweetAlert;

    public function save()
    {
        $this->alert('Saved!', 'Your data was saved successfully.', 'success');
    }
}
```

### Confirm Modal Example

```php
$this->confirm(
    'Are you sure?',
    'This action cannot be undone.',
    'warning',
    'Yes, delete it!',
    'Cancel',
    'deleteConfirmed()', // Make sure this method is not public (should be protected or private)
    'cancelled()'
);
```

### Alert with Callbacks (didClose)

```php
$this->alert(
    'Notice',
    'Check your console.',
    'info',
    [
        'didClose' => '() => { console.log("Alert was closed") }' // Must be a valid JS function string, e.g., '() => { ... }'
    ]
);
```

### Confirm Modal with Callbacks (didOpen, didClose)

```php
$this->confirm(
    'Confirm Action',
    'Do you want to proceed?',
    'question',
    'Proceed',
    'Cancel',
    'proceedConfirmed', // Make sure this method is not public (should be protected or private)
    'cancelledAction',
    [
        'didOpen' => '() => { console.log("Opened!") }',
        'didClose' => '() => { console.log("Closed!") }'
    ]
);
```

## 🧪 Full Demo Example

### Component Class

```php
namespace App\Livewire;

use Livewire\Component;
use Gartservice\LivewireSweetAlert\LivewireSweetAlert;

class SweetAlertDemo extends Component
{
    use LivewireSweetAlert;

    public function showSimpleAlert()
    {
        // Any logic can be here
        $this->alert('Hello!', 'This is a basic alert.', 'success');
    }

    public function showAlertWithCallback()
    {
        $this->alert('Notice', 'Check your console after closing.', 'info', [
            'didClose' => '() => { console.log("Alert was closed!"); console.log("This is a callback function!"); }'
        ]);
    }

    public function showConfirmAlert()
    {
        // Show confirm message before processing
        $this->confirm(
            'Are you sure?',
            'This action is irreversible.',
            'warning',
            'Yes, go ahead!',
            'Cancel',
            'confirmedAction',
            'cancelledAction'
        );
    }
    // This method can be run only after confirm
    protected function confirmedAction()
    {
        // Perform any additional logic after confirmation, such as auditing user actions
        $this->alert('Confirmed', 'You agreed!', 'success');
    }

    public function cancelledAction()
    {
        // Show message if user canceled action
        $this->alert('Cancelled', 'You backed out.', 'error');
    }

    public function render()
    {
        return view('livewire.sweet-alert-demo');
    }
}
```

### Blade View

```blade
<div class="p-6 space-y-4">
    <button type="button" wire:click="showSimpleAlert()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Simple Alert</button>

    <button wire:click="showAlertWithCallback" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">Alert with Callback</button>

    <button wire:click="showConfirmAlert" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Confirmation</button>

    <button wire:click="confirmedAction" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Run Action Directly</button>
</div>
```

## ✅ Compatibility

- Laravel 11+
- PHP 8.3+
- Livewire 3+

## 📄 License

MIT License. Created by [Gartservice](https://github.com/gartservice).
