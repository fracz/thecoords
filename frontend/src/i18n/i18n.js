import Vue from 'vue'
import VueI18n from 'vue-i18n'
import messages from 'yaml-loader!./lang/en.yml'
import axios from '../axios';
import {Settings} from "luxon";

Vue.use(VueI18n);

export const i18n = new VueI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages: {en: messages},
});

const loadedLanguages = ['en'];

function setI18nLanguage(lang) {
    i18n.locale = lang
    Settings.defaultLocale = lang;
    axios.defaults.headers.common['Accept-Language'] = lang
    document.querySelector('html').setAttribute('lang', lang)
    localStorage.setItem('locale', lang);
    return lang;
}

export function loadLanguageAsync(lang) {
    if (i18n.locale === lang) {
        return Promise.resolve(setI18nLanguage(lang))
    }

    if (loadedLanguages.includes(lang)) {
        return Promise.resolve(setI18nLanguage(lang))
    }

    return import(/* webpackChunkName: "lang-[request]" */ `yaml-loader!./lang/${lang}.yml`).then(
        messages => {
            i18n.setLocaleMessage(lang, messages.default);
            loadedLanguages.push(lang);
            return setI18nLanguage(lang);
        }
    )
}

export function setGuiLocale(userData) {
    let locale;
    let match = window.location.href.match(/[?&]lang=([a-z][a-z])/);
    if (match) {
        locale = match[1].substring(0, 2);
    } else if (localStorage.getItem('locale')) {
        locale = localStorage.getItem('locale');
    } else if (userData && userData.locale) {
        locale = userData.locale;
    } else {
        let language = window.navigator.userLanguage || window.navigator.language || 'en';
        locale = language.substring(0, 2);
    }
    const availableLocales = ['en', 'pl'];
    if (!availableLocales.includes(locale)) {
        locale = 'en';
    }
    loadLanguageAsync(locale);
}
