<template>
    <nav>
      <ul class="pagination">
        <li class="page-item">
          <a @click="prePage()" class="page-link">Pre</a>
        </li>
        <div v-for="page in totalPages" :key="page">
          <li class="page-item" :class="{ 'page-item': true, 'hover': page === currentPage }">
            <a @click="changePage(page)" class="page-link">{{ page }}</a>
          </li>
        </div>
        <li class="page-item">
          <a @click="nextPage()" class="page-link"> Next </a>
        </li>
      </ul>
    </nav>
  </template>
  
  <script>
  export default {
    props: {
      totalPages: Number,
      currentPage: Number,
    },
    methods: {
      prePage() {
        this.$emit('page-changed', this.currentPage - 1);
      },
      nextPage() {
        this.$emit('page-changed', this.currentPage + 1);
      },
      changePage(page) {
        this.$emit('page-changed', page);
      },
    },
  };
  </script>

<style scoped>
  .pagination {
    list-style-type: none;
    display: flex;
  }
  .page-item {
    text-align: center;
    border: 1px solid;
  }
  .hover {
    color: #fff;
    background: #000;
  }
  .page-item:hover {
    background-color: rgb(208, 208, 198);
  }
  .page-item a {
    display: block;
    width: 2rem;
    height: 2rem;
    line-height: 2rem;
    cursor: pointer;
  }
</style>