
<style lang="sass">
	@media screen and (max-width: 550px) {
		.content img {
			display: none;
		}
	}
</style>

<template lang="jade">
	div.content
		h2 Основные вкладки

		h3 Календарь

		div.columns
			div.column
				p Полный календарь мероприятий
				ul
					li Ось абсцисс представляет собой временную шкалу.
					li Ось ординат - список всех мест проведения мероприятий.
					li Временную шкалу можно перемещать при помощи колёсика или перетаскивая её мышью.
			div.column
				img(src="public/img/screenshot1.png")

		h3 Мероприятия

		div.columns
			div.column
				p Список всех мероприятий
				ul
					li Доступен удобный поиск и сортировка списка.
					li Конфликты дат проведения мероприятий помечаются красным знаком.
					li Если ожидаемое количество человек, превышает допустимое количество в месте проведения мероприятия, событие отмечается жёлтым знаком.
					li При нажатии на запись откроется форма, в которой можно изменить все данные мероприятия.
					li В форме редактирования мероприятия доступен календарь мероприятий, с функционалом, похожим на основной календарь.
					li При выборе мероприятия в календаре, его данные загрузятся в форму.
			div.column
				img(src="public/img/screenshot2.png")

		h3 Типы

		div.columns
			div.column
				p Список типов мероприятий
				ul
					li Доступен удобный поиск и сортировка списка.
					li При нажатии на запись откроется форма, в которой можно изменить все данные типа.
			div.column
				img(src="public/img/screenshot3.png")

		h3 Аудитория

		div.columns
			div.column
				p Список типов аудитории
				ul
					li Доступен удобный поиск и сортировка списка.
					li При нажатии на запись откроется форма, в которой можно изменить все данные типа.
			div.column
				img(src="public/img/screenshot4.png")

		h3 Места

		div.columns
			div.column
				p Список мест проведения мероприятий
				ul
					li Доступен удобный поиск и сортировка списка.
					li При нажатии на запись откроется форма, в которой можно изменить все данные места.
			div.column
				img(src="public/img/screenshot5.png")

		h3 Отчёты

		div.columns
			div.column
				p Страница для составления отчётов
				ul
					li Все настройки, кроме "Поля отчёта", являются необязательными.
					li После нажатия на кнопку "Составить отчёт", отчёт откроется в новой вкладке, где его можно будет распечатать.
			div.column
				img(src="public/img/screenshot6.png")

		h3 Пользователи

		div.columns
			div.column
				p Список пользователей системы
				ul
					li Доступен удобный поиск и сортировка списка.
					li В таблице можно посмотреть дату регистрации и последнего посещения пользователем системы.
					li При нажатии на запись откроется форма, в которой можно изменить все данные пользователя.
			div.column
				img(src="public/img/screenshot7.png")

</template>

<script>
export default {
	data() {
		return {}
	}
}
</script>