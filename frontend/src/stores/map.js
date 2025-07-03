import { defineStore } from 'pinia';
import axios from '../plugins/axios';

export const useMapStore = defineStore('map', {
    state: () => ({
        mapData: [],
        stats: null,
        validation: null,
        loading: false,
        error: null,
    }),
    actions: {
        // Fetches the map data from the backend API with the given parameters
        async fetchMap(seed, radius, biome_size) {
            this.loading = true;
            this.error = null;
            this.stats = null;
            this.validation = null;
            try {
                const response = await axios.get('/map', {
                    params: {
                        seed: seed,
                        radius: parseInt(radius, 10),
                        biome_size: biome_size,
                    },
                });
                this.mapData = Object.values(response.data.map);
                this.stats = response.data.stats;
                this.validation = response.data.validation || null;
            } catch (error) {
                this.error = 'Failed to fetch map data.';
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
    },
}); 