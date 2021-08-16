<template>
  <div>
    <a @click="showStas">
      <i class="fas fa-plus text-success"></i>
    </a>
    <Modal
      v-model="statsModal"
      title="CLient Details"
      :mask-closable="false"
      :closable="true"
      :width="75"
      :styles="{ top: '20px' }"
    >
    </Modal>
  </div>
</template>
<script>
export default {
  data() {
    return {
      statsModal: false,
    };
  },
  methods: {
    showStas() {
      this.statsModal = true;
    },
  },
};
</script>