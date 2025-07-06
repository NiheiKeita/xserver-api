import { Meta, StoryObj } from '@storybook/react'
// import { waitFor, within } from '@storybook/testing-library';
import { AdminRegister } from '.'

const meta: Meta<typeof AdminRegister> = {
  title: 'views/Admin/AdminRegister',
  component: AdminRegister,
  tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Test: Story = {
  play: async ({ canvasElement }) => {
    // const canvas = within(canvasElement)
    // await waitFor(async () => {
    //   canvas.getByText("QuestionListView")
    // })
  },
}
