<template>
  <b-navbar-dropdown :label="currentLocale.label" :collapsible="true">
    <b-navbar-item v-for="option in availableLocales" :key="option.id" @click="locale = option.id">
      {{ option.label }}
    </b-navbar-item>
  </b-navbar-dropdown>
</template>

<script>
import {loadLanguageAsync} from "@/i18n/i18n";

export default {
  data() {
    return {
      availableLocales: [
        {id: 'en', label: 'EN'},
        {id: 'pl', label: 'PL'},
      ],
    };
  },
  computed: {
    currentLocale() {
      return this.availableLocales.find(({id}) => id === this.locale);
    },
    locale: {
      get() {
        return this.$i18n.locale;
      },
      set(value) {
        loadLanguageAsync(value);
      }
    }
  }
}
</script>

<style lang="scss">
.locale-dropdown .select select {
  border: 0;
}
</style>
