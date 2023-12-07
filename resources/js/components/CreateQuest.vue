<template>
    <div>
      <h1>Create New Quest</h1>
      <form @submit.prevent="createQuest">
        <div class="form-group">
            <label for="title">Title:</label>
            <input v-model="formData.title" type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea v-model="formData.description" id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="reward">Reward:</label>
            <input v-model="formData.reward" type="number" id="reward" name="reward" required>
        </div>
        <div class="form-group">
            <label for="category_id">Category ID:</label>
            <input v-model="formData.category_id" type="number" id="category_id" name="category_id" required>
        </div>
        <div class="form-group">
            <button type="submit">Create Quest</button>
        </div>
      </form>
    </div>
</template>
  
<script>
import axios from 'axios';
export default {
    data() {
        return {
            formData: {
            title: '',
            description: '',
            reward: '',
            category_id: '',
            },
        };
    },
    methods: {
        createQuest() {
            axios.post('/graphql', {
                query: `
                mutation {
                    createQuest(
                    title: "${this.formData.title}"
                    description: "${this.formData.description}"
                    reward: ${this.formData.reward}
                    category_id: ${this.formData.category_id}
                    ) {
                    id
                    title
                    description
                    reward
                    }
                }
                `,
            })
            .then(response => {
                console.log(response.data);
                alert('Quest created successfully!');
            })
            .catch(error => {
                console.error(error);
                alert('Error creating quest.');
            });
        },
    }
}
</script>

<style scoped>
.form-group {
    display: flex;
    width: 30rem;
    margin-bottom: 0.5rem;
}
.form-group label {
    line-height: 2rem;
    flex: 1;
}

.form-group button {
    margin: auto;
}
.form-group input, textarea {
    height: 2rem;
    flex: 3;
}
</style>