<x-modal-window id="contact" title="CONTACT.ME" icon="📡">
    <p class="mb-3 text-muted text-sm-base">{{ __('portfolio.contact_cta') }}</p>
    <div class="flex flex-col gap-2 mb-3">
        <a href="mailto:{{ __('portfolio.info_email') }}" class="btn-accent text-center no-underline">
            {{ __('portfolio.contact_email_btn') }}
        </a>
        <a href="https://ar.linkedin.com/in/alexis-gatica-161838208" class="btn-accent-red text-center no-underline"
            target="_blank">
            {{ __('portfolio.contact_cv_btn') }}
        </a>
    </div>
</x-modal-window>
