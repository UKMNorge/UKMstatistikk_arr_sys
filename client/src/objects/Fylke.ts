import type Kommune from './Kommune';

class Fylke {
    id: number;
    title: string;
    kommuner: Kommune[];

    constructor(id : number, title : string, kommuner : Kommune[] = []) {
        this.id = id;
        this.title = title;
        this.kommuner = kommuner;
    }

    getKommuneById(id : number) : Kommune | undefined {
        return this.kommuner.find(kommune => kommune.id === id);
    }

    addKommune(kommune : Kommune) {
        this.kommuner.push(kommune);
    }
}

export default Fylke;