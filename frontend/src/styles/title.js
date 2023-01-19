import Vue from "vue";

const updatePageTitle = function (title) {
    document.title = title + (title ? ' - ' : '') + 'The Coords';
};

Vue.directive('title', {
    inserted: (el, binding) => updatePageTitle(binding.value || el.innerText),
    update: (el, binding) => updatePageTitle(binding.value || el.innerText),
    componentUpdated: (el, binding) => updatePageTitle(binding.value || el.innerText),
    unbind: () => updatePageTitle(''),
});
