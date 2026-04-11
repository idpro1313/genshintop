export type ElementKey =
  | 'Pyro'
  | 'Hydro'
  | 'Electro'
  | 'Cryo'
  | 'Anemo'
  | 'Geo'
  | 'Dendro';

export function elementBadgeClass(el: ElementKey): string {
  const map: Record<ElementKey, string> = {
    Pyro: 'bg-element-pyro/20 text-element-pyro border-element-pyro/40',
    Hydro: 'bg-element-hydro/20 text-element-hydro border-element-hydro/40',
    Electro: 'bg-element-electro/20 text-element-electro border-element-electro/40',
    Cryo: 'bg-element-cryo/20 text-element-cryo border-element-cryo/40',
    Anemo: 'bg-element-anemo/20 text-element-anemo border-element-anemo/40',
    Geo: 'bg-element-geo/20 text-element-geo border-element-geo/40',
    Dendro: 'bg-element-dendro/20 text-element-dendro border-element-dendro/40',
  };
  return map[el] ?? map.Anemo;
}

export function elementRu(el: ElementKey): string {
  const map: Record<ElementKey, string> = {
    Pyro: 'Пиро',
    Hydro: 'Гидро',
    Electro: 'Электро',
    Cryo: 'Крио',
    Anemo: 'Анемо',
    Geo: 'Гео',
    Dendro: 'Дендро',
  };
  return map[el] ?? el;
}
