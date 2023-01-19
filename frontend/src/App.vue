<template>
  <div id="app-container">
    <div id="wrapper">
      <AppNavbar/>
      <section class="section">
        <RouterView/>
      </section>
    </div>
    <AppFooter/>
  </div>
</template>

<script setup>
import AppFooter from "@/home/AppFooter.vue";
import AppNavbar from "@/home/AppNavbar.vue";</script>