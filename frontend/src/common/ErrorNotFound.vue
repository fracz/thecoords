<template>
  <div id="app-container">
    <div id="wrapper">
      <AppNavbar/>
      <section class="section">
        <div class="container">
          <h1 class="title has-text-centered">{{ $t('Page not found, sorry') }}</h1>
        </div>
      </section>
    </div>
    <AppFooter/>
  </div>
</template>

<script>
import AppNavbar from "@/home/AppNavbar";
import AppFooter from "@/home/AppFooter";

export default {
  name: "ErrorNotFound",
  components: {AppFooter, AppNavbar}
}
</script>
