import { defineStore } from 'pinia';
import axios from '../plugins/axios';

export const useMapStore = defineStore('map', {
    state: () => ({
        mapData: [],
        stats: null,
        loading: false,
        error: null,
    }),
    actions: {
        // Fetches the map data from the backend API with the given parameters
        async fetchMap(seed, radius, biome_size, abundance, scarcity, biomes) {
            this.loading = true;
            this.error = null;
            this.stats = null;
            try {
                const params = {
                    seed: seed,
                    radius: parseInt(radius, 10),
                    biome_size: biome_size,
                    abundance: abundance,
                };
                if (scarcity) {
                    for (const key in scarcity) {
                        params[`scarcity[${key}]`] = scarcity[key];
                    }
                }
                if (biomes) {
                    for (const key in biomes) {
                        params[`biomes[${key}]`] = biomes[key];
                    }
                }
                const response = await axios.get('/map', {
                    params,
                });
                this.mapData = Object.values(response.data.map);
                this.stats = response.data.stats;
            } catch (error) {
                this.error = 'Failed to fetch map data.';
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
    },
}); 