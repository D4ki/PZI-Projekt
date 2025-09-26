<x-layout>
    <x-page-heading>New Job</x-page-heading>

    <x-forms.form method="POST" action="/jobs">
        <x-forms.input label="Title" name="title" placeholder="Programer"></x-forms.input>
        <x-forms.input label="Salary" name="salary" placeholder="2,000 KM"></x-forms.input>
        <x-forms.input label="Location" name="location" placeholder="Mostar, Orašje"></x-forms.input>

        <x-forms.select label="Schedule" name="schedule">
            <option class="text-gray-900">Part Time</option>
            <option class="text-gray-900">Full Time</option>
        </x-forms.select>

        <x-forms.input label="URL" name="url" placeholder="https://acme.com/jobs/ceo-wanted"></x-forms.input>
        <x-forms.checkbox label="Feature (Costs Extra)" name="featured"></x-forms.checkbox>

        <x-forms.divider></x-forms.divider>

        <x-forms.input label="Tags (comma separated)" name="tags" placeholder="programmer, video, education"></x-forms.input>

        <x-forms.button>Publish</x-forms.button>
    </x-forms.form>
</x-layout>