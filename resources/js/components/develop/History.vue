<template>
    <div>
        <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0" v-for="(action, index) in sortedHistory">
            <HistoryItem :action="action" :id="action.id" :step="sortedHistory.length - index"/>
        </div>
        <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-body px-2 py-1">
                <small class="text-muted">Möglicherweise werden manche Daten aufgrund fehlender Rollen-Berechtigung nicht angezeigt.</small>
            </div>
        </div>
    </div>
</template>

<script>
import HistoryItem from "./HistoryItem";

export default {
    components: {HistoryItem},
    props: {
        history: Array
    },
    computed: {
        sortedHistory() {
            let history = [...this.history];

            return history.sort((a, b) => a.created_at < b.created_at ? 1 : -1);
        }
    }
};
</script>
