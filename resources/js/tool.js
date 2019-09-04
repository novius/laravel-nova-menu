Nova.booting((Vue, router, store) => {
    Vue.component('laravel-nova-menu', require('./components/Tool'));
    Vue.component('back-to-menu-tool', require('./components/BackToMenuTool'))
})
