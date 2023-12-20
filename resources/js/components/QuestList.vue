<template>
    <div>
        <div>
            <h2>Quest List</h2>
            <form @submit.prevent="fetchQuests()" style="display: flex;">
                <input style="height: 1.8rem;" v-model="searchValue" type="text" id="search" name="search">
                <button type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="1.5rem" height="1.5rem" viewBox="0 0 30 30">
                    <path d="M 13 3 C 7.4886661 3 3 7.4886661 3 13 C 3 18.511334 7.4886661 23 13 23 C 15.396652 23 17.59741 22.148942 19.322266 20.736328 L 25.292969 26.707031 A 1.0001 1.0001 0 1 0 26.707031 25.292969 L 20.736328 19.322266 C 22.148942 17.59741 23 15.396652 23 13 C 23 7.4886661 18.511334 3 13 3 z M 13 5 C 17.430666 5 21 8.5693339 21 13 C 21 17.430666 17.430666 21 13 21 C 8.5693339 21 5 17.430666 5 13 C 5 8.5693339 8.5693339 5 13 5 z"></path>
                    </svg>
                </button>
            </form>
        </div>
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

        <!-- Use pagination and sort components -->
        <paginate
            :totalPages="this.totalPages"
            :currentPage="this.currentPage"
            @page-changed="handlePageChange"
        />
    </div>
</template>
  
<script>
import Paginate from '@/components/Paginate.vue';
export default {
    components: {
      paginate: Paginate,
    },
    data() {
        return {
            quests: [],
            showModal: false,
            editTitle: '',
            editDescription: '',
            editReward: 0,
            editCategoryId: 0,
            editQuestId: null,
            totalQuests: 0,
            totalePage: 0,
            currentPage: 1,
            perPage: 4,
            sortBy: '',
            sortDesc: false,
            totalPages: 3,
            searchValue: ''
        };
    },
    mounted() {
        this.fetchQuests();
    },
    methods: {
        handlePageChange(page) {
            if(page > 0 && page <= this.totalPages) {
                this.currentPage = page;
            }
            this.fetchQuests();
        },
        fetchQuests() {
            this.sortBy = 'id'
            axios.post('/graphql', {
                    query: `
                    query MyQuery {
                        quests(search: "${this.searchValue}", page: ${this.currentPage}, perPage: ${this.perPage}, sortDesc: ${this.sortDesc}, sortBy: "${this.sortBy}") {
                            id
                            title
                            description
                            reward
                                category {
                                    id
                                    title
                                }
                            }
                        }
                    `,
                })
                .then((response) => {
                    this.quests = response.data.data.quests;
                })
                .catch((error) => {
                    console.error('Error fetching quests:', error);
                });
        },
        onPageChange(newPage) {
            this.currentPage = newPage;
            this.fetchQuests();
        },
        onSortChange({ sortBy, sortDesc }) {
            this.sortBy = sortBy;
            this.sortDesc = sortDesc;
            this.fetchQuests();
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
  