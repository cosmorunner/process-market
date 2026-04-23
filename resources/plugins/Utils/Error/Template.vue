<template>
    <div class="card">
        <div class="card-header text-danger">{{title}}</div>
        <div class="card-body">
            {{body}}
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            options: {
                required: true
            }
        },
        data() {
            return {
                title: this.options.title,
                body: this.options.description
            }
        }
    }
</script>
