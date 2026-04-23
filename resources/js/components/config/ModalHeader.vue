<template>
    <div class="modal-header py-2">
        <h5 class="modal-title" id="exampleModalLabel">{{ title }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>

<script>
export default {
    props: {
        title: String
    }
};
</script>
