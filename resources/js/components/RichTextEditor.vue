<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

interface Props {
    modelValue: string;
    placeholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Write something...',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const editorRef = ref<HTMLDivElement | null>(null);
let quill: Quill | null = null;

onMounted(() => {
    if (editorRef.value) {
        quill = new Quill(editorRef.value, {
            theme: 'snow',
            placeholder: props.placeholder,
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ header: [1, 2, 3, false] }],
                    ['link'],
                    ['clean'],
                ],
            },
        });

        // Set initial content
        if (props.modelValue) {
            quill.root.innerHTML = props.modelValue;
        }

        // Listen for text changes
        quill.on('text-change', () => {
            emit('update:modelValue', quill?.root.innerHTML || '');
        });
    }
});

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
    if (quill && quill.root.innerHTML !== newValue) {
        quill.root.innerHTML = newValue || '';
    }
});
</script>

<template>
    <div class="rich-text-editor">
        <div ref="editorRef" class="min-h-[150px]" />
    </div>
</template>

<style>
.rich-text-editor .ql-container {
    font-family: inherit;
    font-size: 0.875rem;
}

.rich-text-editor .ql-editor {
    min-height: 150px;
}

.rich-text-editor .ql-toolbar {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    background: hsl(var(--muted));
    border-color: hsl(var(--border));
}

.rich-text-editor .ql-container {
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
    border-color: hsl(var(--border));
}

.rich-text-editor .ql-editor.ql-blank::before {
    color: hsl(var(--muted-foreground));
    font-style: normal;
}
</style>

