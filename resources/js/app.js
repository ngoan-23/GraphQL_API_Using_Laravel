import './bootstrap';

import { createApp } from 'vue';
import CreateQuest from './components/CreateQuest.vue';
import QuestList from './components/QuestList.vue';

const app = createApp({});

app.component('create-quest', CreateQuest);
app.component('quest-list', QuestList);

app.mount("#app");