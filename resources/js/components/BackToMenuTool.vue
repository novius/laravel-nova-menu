<template>
    <div class="bg-30 flex items-center px-8 py-4">
        <router-link
                :to="{
                name: 'detail',
                params: {
                    resourceName: 'menus',
                    resourceId: this.panel.fields[0].menu_id
                }
            }"
            class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6"
        >
            {{ this.panel.fields[0].back_trans }}
        </router-link>
    </div>
</template>

<script>
    export default {
        props: [
          'resourceName',
          'resourceId',
          'field',
          'panel'
        ],
    }
</script>
