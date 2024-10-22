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
    'deleteConfirmed()',
    'cancelled()'
);
```

## ✅ Compatibility

- Laravel 11+
- PHP 8.3+
- Livewire 3+

## 📄 License

MIT License. Created by [Gartservice](https://github.com/gartservice).
