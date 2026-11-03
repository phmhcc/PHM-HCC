<form method="POST" action="{{ route('language.update') }}" class="language-switch" aria-label="{{ __('Language') }}">
    @csrf
    <label class="sr-only" for="{{ $id ?? 'language-switch' }}">{{ __('Language') }}</label>
    <select id="{{ $id ?? 'language-switch' }}" name="locale" onchange="this.form.submit()" aria-label="{{ __('Language') }}">
        <option value="en" @selected(app()->getLocale() === 'en')>English</option>
        <option value="sw" @selected(app()->getLocale() === 'sw')>Kiswahili</option>
    </select>
    <noscript><button type="submit">{{ __('Change language') }}</button></noscript>
</form>
