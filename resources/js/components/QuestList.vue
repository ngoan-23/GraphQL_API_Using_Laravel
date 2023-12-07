<template>
    <div>
        <h2>Quest List</h2>
        <table>
            <tr>
                <th width="5%">ID</th>
                <th width="5%">Title</th>
                <th width="50%">Description</th>
                <th width="10%">Reward</th>
                <th width="10%">Category ID</th>
                <th width="20%">Action</th>
            </tr>
            <tr v-for="quest in quests" :key="quest.id">
                <td style="text-align: center;">{{ quest.id }}</td>
                <td style="text-align: center;">{{ quest.title }}</td>
                <td>{{ quest.description }}</td>
                <td style="text-align: center;">{{ quest.reward }}</td>
                <td style="text-align: center;">{{ quest.category.id }}</td>
                <td style="text-align: center;">
                    <button @click="deleteQuest(quest.id)">Delete</button>
                    <button @click="editQuest(quest)">Edit</button>
                </td>
            </tr>
        </table>

        <div v-if="showModal">
            <div class="form-group">
                <label for="title">Title:</label>
                <input v-model="editTitle" placeholder="New Title">
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea v-model="editDescription" id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="reward">Reward:</label>
                <input v-model="editReward" placeholder="New Reward">
            </div>
            <div class="form-group">
                <label for="category_id">Category ID:</label>
                <input v-model="editCategoryId" placeholder="New Category">
            </div>
            <div class="form-group">
                <button @click="updateQuest">Update</button>
            </div>
        </div>
    </div>
</template>
  
<script>
export default {
    data() {
        return {
            quests: [],
            showModal: false,
            editTitle: '',
            editDescription: '',
            editReward: 0,
            editCategoryId: 0,
            editQuestId: null,
        };
    },
    mounted() {
        this.fetchQuests();
    },
    methods: {
        fetchQuests() {
            axios.post('/graphql', {
                query: `query {
                            quests {
                                id
                                title
                                description
                                reward
                                category {
                                    id
                                    title
                                }
                            }
                        }`,
            })
                .then(response => {
                    this.quests = response.data.data.quests;
                })
                .catch(error => {
                    console.error('Error fetching quests:', error);
                });
        },
        editQuest(quest) {
            this.showModal = true;
            this.editTitle = quest.title;
            this.editDescription = quest.description;
            this.editReward = quest.reward;
            this.editCategoryId = quest.category.id;
            this.editQuestId = quest.id;
        },
        updateQuest() {
            axios.post('/graphql', {
                query: `
                mutation {
                    updateQuest(
                            id: ${this.editQuestId}, 
                            title: "${this.editTitle}", 
                            description: "${this.editDescription}",
                            reward: ${parseInt(this.editReward)},
                            category_id: ${parseInt(this.editCategoryId)}
                        ) 
                    {
                        id
                        title
                        description
                        reward
                        category {
                          id
                        }
                    }
                }
                `,
            })
            .then(response => {
                const updatedQuest = response.data.data.updateQuest;
                const index = this.quests.findIndex(quest => quest.id === updatedQuest.id);
                if (index !== -1) {
                    this.$set(this.quests, index, updatedQuest);
                }
                this.showModal = false;
                this.editTitle = '';
                this.editDescription = '';
                this.editReward = '';
                this.editCategoryId = '';
                this.editQuestId = null;
            })
            .catch(error => {
                console.error('Error updating quest:', error);
            });
        },
        deleteQuest(questId) {
            axios.post('/graphql', {
                query: `
                mutation {
                    deleteQuest(id: ${questId})
                }
                `,
            })
                .then(response => {
                    if (response.data.data.deleteQuest) {
                        this.quests = this.quests.filter(quest => quest.id !== questId);
                    }
                })
                .catch(error => {
                    console.error('Error deleting quest:', error);
                });
        },
    }
};
</script>
  
<style scoped>
    table {
        width: 65rem;
    }
    td button {
        margin: 0.5rem;
    }
    
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
  