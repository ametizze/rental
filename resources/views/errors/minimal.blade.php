<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
        /* ... (O longo bloco de CSS de normalização e Tailwind/SystemUI é omitido para brevidade) ... */

        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            line-height: 1.5;
            margin: 0;
            background-color: #f7fafc;
            /* bg-gray-100 */
        }

        .container-center {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .error-code {
            padding-right: 1.5rem;
            font-size: 1.125rem;
            color: #a0aec0;
            /* text-gray-500 */
            border-right: 1px solid #cbd5e0;
            /* border-gray-400 */
        }

        .error-message {
            margin-left: 1rem;
            font-size: 1.125rem;
            color: #a0aec0;
            /* text-gray-500 */
            text-transform: uppercase;
        }
    </style>
</head>

<body class="antialiased">
    <div class="container-center">
        <div>
            <div style="display: flex; align-items: center; padding-top: 2rem;">
                <div class="error-code">
                    @yield('code')
                </div>

                <div class="error-message">
                    @yield('message')
                    {{-- Go back to Home Page --}}
                    <div style="margin-top: 1rem;">
                        <a href="{{ url('/') }}" style="color: #3182ce; text-decoration: none;">
                            &larr; {{ __('Go back to Home Page') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
