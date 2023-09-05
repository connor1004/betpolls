import React, { Component } from 'react';
import {
  Card, Stack, ResourceList, TextStyle
} from '@shopify/polaris';

class Header extends Component {
  render() {
    return (
      <Card>
        <ResourceList.Item>
          <Stack>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '50px' }}>ID</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '50px' }}>Ref id</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '60px' }}>Logo</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '200px' }}>Name</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '200px' }}>Name ES</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '200px' }}>Short name</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '200px' }}>Short name ES</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '140px' }}>Action</div>
              </TextStyle>
            </Stack.Item>
          </Stack>
        </ResourceList.Item>
      </Card>
    );
  }
}

export default Header;
