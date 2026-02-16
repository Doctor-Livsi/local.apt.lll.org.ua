import { createApp } from 'vue'
import ApteksCounterWidget from './components/widgets/ApteksCounterWidget.vue'
import './echo' // важно: подключаем Echo/Reverb

createApp(ApteksCounterWidget).mount('#wsCounterApp')
