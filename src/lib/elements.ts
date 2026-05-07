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

/** Левая акцентная полоса на карточке — цвет стихии. */
export function elementCardAccentClass(el: ElementKey): string {
  const map: Record<ElementKey, string> = {
    Pyro: 'border-l-element-pyro shadow-[0_0_20px_rgba(255,107,74,0.2)]',
    Hydro: 'border-l-element-hydro shadow-[0_0_20px_rgba(79,195,247,0.2)]',
    Electro: 'border-l-element-electro shadow-[0_0_20px_rgba(179,136,255,0.2)]',
    Cryo: 'border-l-element-cryo shadow-[0_0_20px_rgba(129,212,250,0.2)]',
    Anemo: 'border-l-element-anemo shadow-[0_0_20px_rgba(105,240,174,0.2)]',
    Geo: 'border-l-element-geo shadow-[0_0_20px_rgba(255,213,79,0.2)]',
    Dendro: 'border-l-element-dendro shadow-[0_0_20px_rgba(165,214,167,0.2)]',
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
