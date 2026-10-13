/**
 * ============================================================
 * ZAKKATPRO GLOBAL JAVASCRIPT
 * ============================================================
 *
 * Responsibilities:
 *
 * 1. Mobile navigation
 * 2. Dark / light mode
 * 3. Language dropdown
 * 4. Language persistence
 * 5. RTL / LTR support
 * 6. Hero slider
 * 7. Accessibility support
 *
 * This file contains GLOBAL behaviour only.
 * Page-specific functionality should remain inside its
 * own page/module when necessary.
 *
 * ============================================================
 */

document.addEventListener("DOMContentLoaded", () => {

    /*
    |--------------------------------------------------------------------------
    | STORAGE KEYS
    |--------------------------------------------------------------------------
    */

    const LANGUAGE_KEY = "zakkatpro_language";
    const THEME_KEY = "zakkatpro_theme";


    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const html = document.documentElement;

    const languageButton = document.getElementById("languageButton");
    const languageMenu = document.getElementById("languageMenu");
    const currentLanguage = document.getElementById("currentLanguage");
    const languageOptions = document.querySelectorAll(".language-option");

    const themeToggle = document.getElementById("themeToggle");
    const mobileThemeToggle = document.getElementById("mobileThemeToggle");
    const themeIcon = document.getElementById("themeIcon");

    const mobileMenuButton = document.getElementById("mobileMenuButton");
    const mobileMenu = document.getElementById("mobileMenu");
    const mobileMenuIcon = document.getElementById("mobileMenuIcon");


    /*
    |--------------------------------------------------------------------------
    | LANGUAGE DATA
    |--------------------------------------------------------------------------
    |
    | This is the temporary global UI dictionary.
    |
    | Later, when we fully connect:
    |
    | language/en.php
    | language/ha.php
    | language/ar.php
    |
    | we can move the translation source to PHP/database.
    |
    |--------------------------------------------------------------------------
    */

    const languages = {

        en: {
            name: "English",
            direction: "ltr"
        },

        ha: {
            name: "Hausa",
            direction: "ltr"
        },

        ar: {
            name: "العربية",
            direction: "rtl"
        }

    };


    /*
    |--------------------------------------------------------------------------
    | GET SAVED LANGUAGE
    |--------------------------------------------------------------------------
    */

    function getSavedLanguage() {

        const savedLanguage = localStorage.getItem(LANGUAGE_KEY);

        if (savedLanguage && languages[savedLanguage]) {
            return savedLanguage;
        }

        return "en";
    }


    /*
    |--------------------------------------------------------------------------
    | APPLY LANGUAGE
    |--------------------------------------------------------------------------
    */

    function applyLanguage(language) {

        if (!languages[language]) {
            language = "en";
        }

        const languageData = languages[language];

        /*
        | HTML language
        */

        html.setAttribute("lang", language);


        /*
        | Text direction
        */

        html.setAttribute("dir", languageData.direction);


        /*
        | Current language label
        */

        if (currentLanguage) {
            currentLanguage.textContent = languageData.name;
        }


        /*
        | Save only for THIS browser/user.
        |
        | This does NOT change the language for other users.
        */

        localStorage.setItem(LANGUAGE_KEY, language);


        /*
        | Mark selected language
        */

        languageOptions.forEach(option => {

            const optionLanguage = option.dataset.language;

            if (optionLanguage === language) {

                option.classList.add(
                    "bg-emerald-50",
                    "text-emerald-700",
                    "font-semibold"
                );

                option.classList.remove(
                    "text-slate-700"
                );

            } else {

                option.classList.remove(
                    "bg-emerald-50",
                    "text-emerald-700",
                    "font-semibold"
                );

            }

        });


        /*
        | Close dropdown
        */

        closeLanguageMenu();
    }


    /*
    |--------------------------------------------------------------------------
    | LANGUAGE BUTTON
    |--------------------------------------------------------------------------
    */

    function openLanguageMenu() {

        if (!languageMenu) {
            return;
        }

        languageMenu.classList.remove("hidden");

        if (languageButton) {
            languageButton.setAttribute(
                "aria-expanded",
                "true"
            );
        }
    }


    function closeLanguageMenu() {

        if (!languageMenu) {
            return;
        }

        languageMenu.classList.add("hidden");

        if (languageButton) {
            languageButton.setAttribute(
                "aria-expanded",
                "false"
            );
        }
    }


    function toggleLanguageMenu() {

        if (!languageMenu) {
            return;
        }

        const isHidden = languageMenu.classList.contains("hidden");

        if (isHidden) {
            openLanguageMenu();
        } else {
            closeLanguageMenu();
        }
    }


    if (languageButton) {

        languageButton.addEventListener(
            "click",
            event => {

                event.stopPropagation();

                toggleLanguageMenu();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | LANGUAGE OPTIONS
    |--------------------------------------------------------------------------
    */

    languageOptions.forEach(option => {

        option.addEventListener(
            "click",
            () => {

                const selectedLanguage =
                    option.dataset.language;

                if (!selectedLanguage) {
                    return;
                }

                applyLanguage(selectedLanguage);

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | CLOSE LANGUAGE MENU WHEN CLICKING OUTSIDE
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        "click",
        event => {

            if (
                languageMenu &&
                languageButton &&
                !languageMenu.contains(event.target) &&
                !languageButton.contains(event.target)
            ) {

                closeLanguageMenu();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | THEME
    |--------------------------------------------------------------------------
    */

    function getSavedTheme() {

        const savedTheme =
            localStorage.getItem(THEME_KEY);

        if (
            savedTheme === "dark" ||
            savedTheme === "light"
        ) {

            return savedTheme;

        }

        /*
        | Default:
        | Follow system preference.
        */

        if (
            window.matchMedia &&
            window.matchMedia(
                "(prefers-color-scheme: dark)"
            ).matches
        ) {

            return "dark";

        }

        return "light";
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE THEME ICON
    |--------------------------------------------------------------------------
    */

    function updateThemeIcon(theme) {

        if (!themeIcon) {
            return;
        }

        if (theme === "dark") {

            themeIcon.classList.remove(
                "fa-moon"
            );

            themeIcon.classList.add(
                "fa-sun"
            );

        } else {

            themeIcon.classList.remove(
                "fa-sun"
            );

            themeIcon.classList.add(
                "fa-moon"
            );

        }
    }


    /*
    |--------------------------------------------------------------------------
    | APPLY THEME
    |--------------------------------------------------------------------------
    */

    function applyTheme(theme) {

        if (theme === "dark") {

            html.classList.add("dark");

        } else {

            html.classList.remove("dark");

        }

        localStorage.setItem(
            THEME_KEY,
            theme
        );

        updateThemeIcon(theme);
    }


    /*
    |--------------------------------------------------------------------------
    | TOGGLE THEME
    |--------------------------------------------------------------------------
    */

    function toggleTheme() {

        const currentTheme =
            html.classList.contains("dark")
                ? "dark"
                : "light";

        const nextTheme =
            currentTheme === "dark"
                ? "light"
                : "dark";

        applyTheme(nextTheme);
    }


    if (themeToggle) {

        themeToggle.addEventListener(
            "click",
            toggleTheme
        );

    }


    if (mobileThemeToggle) {

        mobileThemeToggle.addEventListener(
            "click",
            toggleTheme
        );

    }


    /*
    |--------------------------------------------------------------------------
    | MOBILE MENU
    |--------------------------------------------------------------------------
    */

    function openMobileMenu() {

        if (!mobileMenu) {
            return;
        }

        mobileMenu.classList.remove("hidden");

        if (mobileMenuButton) {

            mobileMenuButton.setAttribute(
                "aria-expanded",
                "true"
            );

        }

        if (mobileMenuIcon) {

            mobileMenuIcon.classList.remove(
                "fa-bars"
            );

            mobileMenuIcon.classList.add(
                "fa-xmark"
            );

        }
    }


    function closeMobileMenu() {

        if (!mobileMenu) {
            return;
        }

        mobileMenu.classList.add("hidden");

        if (mobileMenuButton) {

            mobileMenuButton.setAttribute(
                "aria-expanded",
                "false"
            );

        }

        if (mobileMenuIcon) {

            mobileMenuIcon.classList.remove(
                "fa-xmark"
            );

            mobileMenuIcon.classList.add(
                "fa-bars"
            );

        }
    }


    function toggleMobileMenu() {

        if (!mobileMenu) {
            return;
        }

        const isHidden =
            mobileMenu.classList.contains("hidden");

        if (isHidden) {

            openMobileMenu();

        } else {

            closeMobileMenu();

        }
    }


    if (mobileMenuButton) {

        mobileMenuButton.addEventListener(
            "click",
            toggleMobileMenu
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE MOBILE MENU AFTER NAVIGATION
    |--------------------------------------------------------------------------
    */

    const mobileLinks =
        document.querySelectorAll(
            ".mobile-nav-link"
        );

    mobileLinks.forEach(link => {

        link.addEventListener(
            "click",
            closeMobileMenu
        );

    });


    /*
    |--------------------------------------------------------------------------
    | CLOSE MOBILE MENU WHEN WINDOW BECOMES DESKTOP
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        "resize",
        () => {

            if (window.innerWidth >= 1024) {
                closeMobileMenu();
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | HERO SLIDER
    |--------------------------------------------------------------------------
    |
    | Only runs on pages that actually contain:
    |
    | .slide
    |
    | Therefore it will not cause errors on other pages.
    |
    |--------------------------------------------------------------------------
    */

    const slides =
        document.querySelectorAll(".slide");

    const dots =
        document.querySelectorAll(".dot");

    const previousButton =
        document.getElementById("previousSlide");

    const nextButton =
        document.getElementById("nextSlide");

    let currentSlide = 0;
    let sliderTimer = null;


    function showSlide(index) {

        if (!slides.length) {
            return;
        }

        if (index >= slides.length) {
            index = 0;
        }

        if (index < 0) {
            index = slides.length - 1;
        }

        currentSlide = index;


        slides.forEach(
            slide => {
                slide.classList.add("hidden");
            }
        );


        dots.forEach(
            dot => {

                dot.classList.remove(
                    "opacity-100"
                );

                dot.classList.add(
                    "opacity-50"
                );

            }
        );


        slides[currentSlide].classList.remove(
            "hidden"
        );


        if (dots[currentSlide]) {

            dots[currentSlide].classList.remove(
                "opacity-50"
            );

            dots[currentSlide].classList.add(
                "opacity-100"
            );

        }

    }


    function nextSlide() {

        showSlide(currentSlide + 1);

    }


    function previousSlide() {

        showSlide(currentSlide - 1);

    }


    function startSlider() {

        if (slides.length <= 1) {
            return;
        }

        stopSlider();

        sliderTimer = setInterval(
            nextSlide,
            5000
        );

    }


    function stopSlider() {

        if (sliderTimer !== null) {

            clearInterval(
                sliderTimer
            );

            sliderTimer = null;

        }

    }


    if (slides.length > 0) {

        showSlide(0);

        startSlider();


        /*
        | Dot navigation
        */

        dots.forEach(
            (dot, index) => {

                dot.addEventListener(
                    "click",
                    () => {

                        showSlide(index);

                        startSlider();

                    }
                );

            }
        );


        /*
        | Previous button
        */

        if (previousButton) {

            previousButton.addEventListener(
                "click",
                () => {

                    previousSlide();

                    startSlider();

                }
            );

        }


        /*
        | Next button
        */

        if (nextButton) {

            nextButton.addEventListener(
                "click",
                () => {

                    nextSlide();

                    startSlider();

                }
            );

        }


        /*
        | Pause when browser tab is hidden
        */

        document.addEventListener(
            "visibilitychange",
            () => {

                if (document.hidden) {

                    stopSlider();

                } else {

                    startSlider();

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIALISE GLOBAL SETTINGS
    |--------------------------------------------------------------------------
    */

    applyLanguage(
        getSavedLanguage()
    );

    applyTheme(
        getSavedTheme()
    );


    /*
    |--------------------------------------------------------------------------
    | REDUCED MOTION
    |--------------------------------------------------------------------------
    */

    const reducedMotion =
        window.matchMedia &&
        window.matchMedia(
            "(prefers-reduced-motion: reduce)"
        ).matches;

    if (reducedMotion) {
        stopSlider();
    }


    /*
    |--------------------------------------------------------------------------
    | DEBUG MESSAGE
    |--------------------------------------------------------------------------
    |
    | Remove later before production if desired.
    |--------------------------------------------------------------------------
    */

    console.log(
        "ZAKKATPRO global JavaScript initialized."
    );

});