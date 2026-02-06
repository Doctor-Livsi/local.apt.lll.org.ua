import { createApp } from 'vue'
import ApteksCounterConnected from './components/Widget/ApteksCounterConnected.vue'
import './echo' // важно: подключаем Echo/Reverb

createApp(ApteksCounterConnected).mount('#wsCounterApp')
