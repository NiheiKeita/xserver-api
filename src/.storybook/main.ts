import type { StorybookConfig } from "@storybook/react-vite"

const config: StorybookConfig = {
    stories: [
        "../resources/**/*.stories.@(js|jsx|mjs|ts|tsx)",
        "../stories/**/*.stories.@(js|jsx|mjs|ts|tsx)",
    ],
    addons: [
        "@storybook/addon-onboarding",
        "@storybook/addon-links",
        "@storybook/addon-essentials",
        "@chromatic-com/storybook",
        "@storybook/addon-interactions",
    ],
    framework: {
        name: "@storybook/react-vite",
        options: {},
    },
    staticDirs: ["../public"],
}
export default config
