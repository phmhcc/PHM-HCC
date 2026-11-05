<style>
    .auth-section {
        display: grid;
        min-height: 100vh;
        place-items: center;
        padding: 48px 20px;
        background: #f4f7fb;
    }

    .auth-card {
        width: min(100%, 480px);
        padding: 38px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .1);
    }

    .auth-logo {
        display: block;
        width: 72px;
        height: 72px;
        margin: 0 auto 16px;
        border-radius: 50%;
        object-fit: cover;
    }

    .auth-card h1 {
        margin: 0 0 12px;
        color: #003366;
        text-align: center;
    }

    .auth-card > p {
        color: #555;
        line-height: 1.7;
    }

    .auth-form {
        margin-top: 24px;
    }

    .auth-form label {
        display: block;
        margin: 14px 0 6px;
        color: #003366;
        font-weight: 600;
    }

    .auth-form input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccd4df;
        border-radius: 6px;
        font: inherit;
    }

    .auth-password-wrapper {
        position: relative;
    }

    .auth-form .auth-password-wrapper input {
        padding-right: 44px;
    }

    .auth-form .auth-password-toggle {
        position: absolute;
        top: 50%;
        right: 12px;
        display: grid;
        width: auto;
        margin: 0;
        padding: 4px;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #777;
        cursor: pointer;
    }

    .auth-password-toggle:focus-visible {
        outline: 2px solid #003366;
        outline-offset: 2px;
    }

    .auth-form button {
        width: 100%;
        margin-top: 22px;
        padding: 13px;
        border: 0;
        border-radius: 6px;
        background: #003366;
        color: white;
        font: inherit;
        font-weight: 600;
        cursor: pointer;
    }

    .auth-card a {
        display: inline-block;
        margin-top: 16px;
        color: #003366;
    }

    .auth-message {
        margin-top: 16px;
        color: #176b3a !important;
    }

    .auth-error {
        margin: 6px 0 0;
        color: #b42318 !important;
        font-size: 14px;
    }

    @media (max-width: 600px) {
        .auth-card {
            padding: 28px 22px;
        }
    }
</style>
